{{-- File: resources/views/customers/_modal.blade.php --}}

<div x-show="showModal"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
    x-transition
    @keydown.escape.window="showModal = false">

    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto"
        @click.stop>

        <div class="flex items-center justify-between px-6 py-4 border-b sticky top-0 bg-white">
            <h3 class="text-lg font-bold text-gray-900"
                x-text="editingId ? 'Edit Customer' : 'New Customer'"></h3>
            <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-xl">✕</button>
        </div>

        <form @submit.prevent="saveCustomer()" class="px-6 py-5 space-y-4">

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                        x-model="form.name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                                  focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        :class="{'border-red-400': errors.name}"
                        placeholder="Customer full name">
                    <p x-show="errors.name" class="text-red-500 text-xs mt-1" x-text="errors.name"></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text"
                        x-model="form.phone"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                                  focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        placeholder="+1-555-0000">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email"
                        x-model="form.email"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                                  focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        placeholder="customer@email.com">
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <input type="text"
                        x-model="form.address"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                                  focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                    <input type="text"
                        x-model="form.city"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                                  focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                    <select x-model="form.gender"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="">Select...</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                        <option value="prefer_not_to_say">Prefer not to say</option>
                    </select>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea x-model="form.notes"
                        rows="2"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                                     focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        placeholder="Internal notes..."></textarea>
                </div>

                <div class="col-span-2 flex items-center gap-2">
                    <input type="checkbox" x-model="form.is_active" id="customerActive" class="rounded">
                    <label for="customerActive" class="text-sm text-gray-700">Active customer</label>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button"
                    @click="showModal = false"
                    class="flex-1 py-2.5 border border-gray-300 rounded-xl text-sm
                               text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit"
                    :disabled="saving"
                    class="flex-1 py-2.5 bg-blue-600 text-white rounded-xl text-sm
                               font-semibold hover:bg-blue-700 disabled:opacity-50">
                    <span x-show="saving">Saving...</span>
                    <span x-show="!saving" x-text="editingId ? 'Update Customer' : 'Create Customer'"></span>
                </button>
            </div>
        </form>
    </div>
</div>