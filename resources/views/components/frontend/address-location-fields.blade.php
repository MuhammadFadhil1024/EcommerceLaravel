@props([
    'provinceOptions' => [],
    'cityOptions' => [],
    'districtOptions' => [],
])

<div x-data="{
    open: false,
    query: @entangle('provinceSearch'),
    matches(name) {
        if (!this.query) return true;
        return name.toLowerCase().includes(this.query.toLowerCase());
    },
    hasVisibleOptions() {
        if (!this.$refs.provinceList) return false;

        return Array.from(this.$refs.provinceList.querySelectorAll('[data-option]'))
            .some(el => el.offsetParent !== null);
    },
    choose(id, name) {
        this.query = name;
        this.open = false;
        $wire.selectProvince(id);
        this.$refs.provinceInput.blur();
    }
}" class="relative">
    <label for="provinceSearch" class="mb-2 block text-sm font-semibold text-gray-700">Provinsi</label>

    <input id="provinceSearch" type="text" x-ref="provinceInput" x-model="query" @focus="open = true" @input="open = true"
        placeholder="Cari dan pilih provinsi" autocomplete="off"
        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-gray-900 focus:border-pink-400 focus:outline-none focus:ring-2 focus:ring-pink-200" />

    <div x-cloak x-show="open" x-ref="provinceList"
        class="absolute z-10 mt-2 max-h-56 w-full overflow-y-auto rounded-xl border border-gray-200 bg-white shadow-lg">
        @foreach ($provinceOptions as $provinceOption)
            <button type="button" x-show="matches(@js($provinceOption['name']))"
                @click.prevent.stop="choose({{ (int) $provinceOption['id'] }}, @js($provinceOption['name']))" data-option
                class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100">{{ $provinceOption['name'] }}</button>
        @endforeach

        <div x-show="!hasVisibleOptions()" class="px-4 py-3 text-sm text-gray-500">
            Provinsi tidak ditemukan.
        </div>
    </div>
</div>

@error('provinceSearch')
    <p class="-mt-2 text-sm text-red-500">{{ $message }}</p>
@enderror

<div x-data="{
    open: false,
    query: @entangle('citySearch'),
    matches(name) {
        if (!this.query) return true;
        return name.toLowerCase().includes(this.query.toLowerCase());
    },
    hasVisibleOptions() {
        if (!this.$refs.cityList) return false;

        return Array.from(this.$refs.cityList.querySelectorAll('[data-option]'))
            .some(el => el.offsetParent !== null);
    },
    choose(id, name) {
        this.query = name;
        this.open = false;
        $wire.selectCity(id, name);
        this.$refs.cityInput.blur();
    }
}" class="relative">
    <label for="citySearch" class="mb-2 block text-sm font-semibold text-gray-700">City</label>

    <input id="citySearch" type="text" x-ref="cityInput" x-model="query" @focus="open = true; $wire.onCityFieldFocus()" @input="open = true"
        placeholder="Cari dan pilih kota" autocomplete="off"
        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-gray-900 focus:border-pink-400 focus:outline-none focus:ring-2 focus:ring-pink-200" />

    <div x-cloak x-show="open" x-ref="cityList"
        class="absolute z-10 mt-2 max-h-56 w-full overflow-y-auto rounded-xl border border-gray-200 bg-white shadow-lg">
        @foreach ($cityOptions as $cityOption)
            <button type="button" x-show="matches(@js($cityOption['name']))"
                @click.prevent.stop="choose({{ (int) $cityOption['id'] }}, @js($cityOption['name']))" data-option
                class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100">{{ $cityOption['name'] }}</button>
        @endforeach

        <div x-show="!hasVisibleOptions()" class="px-4 py-3 text-sm text-gray-500">
            Kota tidak ditemukan.
        </div>
    </div>
</div>

@error('citySearch')
    <p class="-mt-2 text-sm text-red-500">{{ $message }}</p>
@enderror

<div x-data="{
    open: false,
    query: @entangle('districtSearch'),
    matches(name) {
        if (!this.query) return true;
        return name.toLowerCase().includes(this.query.toLowerCase());
    },
    hasVisibleOptions() {
        if (!this.$refs.districtList) return false;

        return Array.from(this.$refs.districtList.querySelectorAll('[data-option]'))
            .some(el => el.offsetParent !== null);
    },
    choose(id, name) {
        this.query = name;
        this.open = false;
        $wire.selectDistrict(id, name);
        this.$refs.districtInput.blur();
    }
}" class="relative">
    <label for="districtSearch" class="mb-2 block text-sm font-semibold text-gray-700">District</label>

    <input id="districtSearch" type="text" x-ref="districtInput" x-model="query" @focus="open = true; $wire.onDistrictFieldFocus()" @input="open = true"
        placeholder="Cari dan pilih kecamatan" autocomplete="off"
        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-gray-900 focus:border-pink-400 focus:outline-none focus:ring-2 focus:ring-pink-200" />

    <div x-cloak x-show="open" x-ref="districtList"
        class="absolute z-10 mt-2 max-h-56 w-full overflow-y-auto rounded-xl border border-gray-200 bg-white shadow-lg">
        @foreach ($districtOptions as $districtOption)
            <button type="button" x-show="matches(@js($districtOption['name']))"
                @click.prevent.stop="choose({{ (int) $districtOption['id'] }}, @js($districtOption['name']))" data-option
                class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100">{{ $districtOption['name'] }}</button>
        @endforeach

        <div x-show="!hasVisibleOptions()" class="px-4 py-3 text-sm text-gray-500">
            Kecamatan tidak ditemukan.
        </div>
    </div>
</div>

@error('districtSearch')
    <p class="-mt-2 text-sm text-red-500">{{ $message }}</p>
@enderror
