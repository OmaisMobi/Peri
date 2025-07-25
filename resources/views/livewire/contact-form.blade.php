<div id="contact" class="py-16 bg-[#f8f9fa]">
    <div class="container mx-auto px-6 lg:px-16">
        <div class="text-center mb-12">
            <div class="inline-block tab px-4 py-1 text-sm font-bold relative">
                <span class="absolute left-0 top-0 h-full w-1 bg-[var(--primary)]"></span>
                CONTACT US
            </div>
            <h2 class="text-4xl font-bold mt-4 text-gray-800">Get in Touch</h2>
        </div>

        <div class="flex flex-col lg:flex-row bg-white shadow-lg rounded-lg overflow-hidden">
            <!-- Left Side: Image -->
            <div class="hidden lg:block lg:w-1/2">
                <img src="https://images.unsplash.com/photo-1487017159836-4e23ece2e4cf?q=80&w=2071&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                    alt="Contact Image" class="w-full h-full object-cover">
            </div>

            <!-- Right Side: Contact Form -->
            <div class="w-full lg:w-1/2 p-8">
                @if (session()->has('success'))
                    <div class="bg-green-500 text-white p-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <form wire:submit.prevent="submit" class="space-y-6 mt-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <input type="text" wire:model.defer="name" placeholder="Your Name"
                                class="w-full p-3 rounded border border-gray-300 focus:ring-2 focus:ring-[var(--primary)] focus:outline-none">
                            @error('name')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <input type="email" wire:model.defer="email" placeholder="Your Email"
                                class="w-full p-3 rounded border border-gray-300 focus:ring-2 focus:ring-[var(--primary)] focus:outline-none">
                            @error('email')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <textarea wire:model.defer="message" placeholder="Your Message" rows="5"
                            class="w-full p-3 rounded border border-gray-300 focus:ring-2 focus:ring-[var(--primary)] focus:outline-none"></textarea>
                        @error('message')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="text-center">
                        <button type="submit"
                            class="bg-[var(--secondary)] text-[var(--primary)] font-semibold py-3 px-8 rounded-lg hover:bg-[var(--primary)] hover:text-white transition duration-300 shadow-lg">
                            Send Message →
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
