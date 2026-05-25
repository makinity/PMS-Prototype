{{-- Real-time notification listener (include before </body> in all layouts) --}}
<script>
(function() {
    const userId = document.querySelector('meta[name="auth-user-id"]')?.getAttribute('content');
    const debugPrefix = '[PMS RT]';

    if (!userId) {
        console.warn(`${debugPrefix} Missing auth user id meta tag; realtime listener not started.`);
        return;
    }

    const channelName = `App.Models.User.${userId}`;
    const soundPath = '/sounds/notifications/new-notification.wav';
    let canPlayNotificationSound = false;
    let notificationAudio = null;

    const createNotificationAudio = () => {
        if (!notificationAudio) {
            notificationAudio = new Audio(soundPath);
            notificationAudio.preload = 'auto';
            notificationAudio.volume = 0.8;
        }
        return notificationAudio;
    };

    const playNotificationSound = () => {
        try {
            if (!canPlayNotificationSound) return;
            const audio = createNotificationAudio();
            audio.currentTime = 0;
            audio.play().catch(() => {});
        } catch (e) {}
    };

    const unlockNotificationSound = () => {
        const audio = createNotificationAudio();
        // Create and resume an AudioContext to satisfy autoplay policy
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        if (ctx.state === 'suspended') ctx.resume();
        // Try playing a silent moment to fully unlock the audio element
        audio.play().then(() => {
            audio.pause();
            audio.currentTime = 0;
        }).catch(() => {});
        // Mark as unlocked regardless — the user gesture satisfies autoplay policy
        canPlayNotificationSound = true;
        console.info(`${debugPrefix} Audio unlocked.`);
    };

    const bindAudioUnlock = () => {
        const events = ['pointerdown', 'keydown', 'touchstart'];
        const handler = () => {
            unlockNotificationSound();
            events.forEach(e => document.removeEventListener(e, handler, true));
        };
        events.forEach(e => document.addEventListener(e, handler, true));
    };

    const subscribe = () => {
        console.info(`${debugPrefix} Subscribing to private channel:`, channelName);

        const pusher = window.Echo?.connector?.pusher;
        if (pusher?.connection) {
            console.info(`${debugPrefix} Pusher state:`, pusher.connection.state);
            pusher.connection.bind('state_change', (states) => {
                console.info(`${debugPrefix} Pusher state_change:`, states.previous, '→', states.current);
            });
            pusher.connection.bind('connected', () => {
                console.info(`${debugPrefix} ✅ WebSocket CONNECTED`);
            });
            pusher.connection.bind('error', (err) => {
                console.error(`${debugPrefix} ❌ Pusher error:`, err);
            });
        } else {
            console.warn(`${debugPrefix} Echo connector/pusher not found.`);
        }

        const channel = window.Echo.private(channelName);

        channel.notification((notification) => {
            console.info(`${debugPrefix} 🔔 Notification received:`, notification);
            // Small delay to ensure DB write is committed before Livewire fetches
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent('pms-notification-received'));
            }, 300);
            playNotificationSound();
        });

        // Debug: listen for subscription success/error
        if (channel.subscription) {
            channel.subscription.bind('pusher:subscription_succeeded', () => {
                console.info(`${debugPrefix} ✅ Channel subscription succeeded:`, channelName);
            });
            channel.subscription.bind('pusher:subscription_error', (err) => {
                console.error(`${debugPrefix} ❌ Channel subscription error:`, channelName, err);
            });
        }
    };

    // Expose debug helper globally
    window.__pmsRealtimeDebug = {
        userId,
        channelName,
        emitTestEvent() {
            window.dispatchEvent(new CustomEvent('pms-notification-received'));
            console.info(`${debugPrefix} Manual pms-notification-received event emitted`);
        },
        getEchoState() {
            const pusher = window.Echo?.connector?.pusher;
            return {
                echoExists: !!window.Echo,
                pusherExists: !!pusher,
                connectionState: pusher?.connection?.state ?? 'N/A',
                channels: pusher?.channels?.channels ? Object.keys(pusher.channels.channels) : [],
            };
        }
    };
    console.info(`${debugPrefix} Debug helper ready at window.__pmsRealtimeDebug`);

    const waitForEchoAndSubscribe = () => {
        let attempts = 0;
        const interval = setInterval(() => {
            attempts++;
            if (window.Echo) {
                clearInterval(interval);
                subscribe();
                return;
            }
            if (attempts === 1) console.info(`${debugPrefix} Waiting for window.Echo...`);
            if (attempts >= 40) {
                clearInterval(interval);
                console.error(`${debugPrefix} ❌ window.Echo not available after 10s. Broadcasting will NOT work.`);
            }
        }, 250);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', waitForEchoAndSubscribe, { once: true });
        document.addEventListener('DOMContentLoaded', bindAudioUnlock, { once: true });
    } else {
        waitForEchoAndSubscribe();
        bindAudioUnlock();
    }
})();
</script>
