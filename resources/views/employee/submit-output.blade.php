<x-layouts.employee>
    <section class="space-y-6">

        <div>
            <h1 class="text-xl font-semibold">Submit Output</h1>
            <p class="text-sm text-gray-500">
                Upload completed outputs for validation and review.
            </p>
        </div>

        <div class="border rounded-lg p-4 space-y-4">

            <div>
                <label class="text-sm font-medium">Related Task / Output</label>
                <select class="w-full mt-1 border rounded px-3 py-2">
                    <option>Select task</option>
                    <option>Report Generation – ABC Corp</option>
                    <option>Client Form Review – XYZ Ltd</option>
                </select>
            </div>

            <div>
                <label class="text-sm font-medium">Upload File</label>
                <input type="file" class="w-full mt-1 border rounded px-3 py-2">
            </div>

            <div>
                <label class="text-sm font-medium">Remarks</label>
                <textarea rows="3"
                          class="w-full mt-1 border rounded px-3 py-2"
                          placeholder="Additional notes or explanation"></textarea>
            </div>

            <div class="flex justify-end gap-2">
                <button class="px-4 py-2 border rounded">Cancel</button>
                <button class="px-4 py-2 bg-blue-600 text-white rounded">
                    Submit Output
                </button>
            </div>

        </div>

        <p class="text-xs text-gray-500">
            Submitted outputs are subject to supervisor review and contribute to IPCR ratings.
        </p>

    </section>
</x-layouts.employee>
