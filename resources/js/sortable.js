let favoriteSortable = null;

function destroyFavoriteSortable() {
    if (favoriteSortable) {
        favoriteSortable.destroy();
        favoriteSortable = null;
    }
}

function initFavoriteSortable() {
    destroyFavoriteSortable();

    const list = document.querySelector("[data-favorite-list]");

    if (!list) {
        return;
    }

    if (!window.Sortable) {
        console.error("SortableJS belum tersedia.");
        return;
    }

    favoriteSortable = new window.Sortable(list, {
        animation: 200,

        // Elemen yang boleh dipindahkan.
        draggable: "[data-favorite-id]",

        // Hanya handle ini yang memulai drag.
        handle: "[data-drag-handle]",

        ghostClass: "opacity-40",
        chosenClass: "ring-2",
        dragClass: "opacity-70",

        onStart() {
            list.classList.add("select-none");
        },

        onEnd() {
            list.classList.remove("select-none");

            const ids = Array.from(
                list.querySelectorAll("[data-favorite-id]"),
            ).map((item) => Number(item.dataset.favoriteId));

            if (!ids.length) {
                return;
            }

            const wireId = list.closest("[wire\\:id]")?.getAttribute("wire:id");

            if (!wireId) {
                console.error("wire:id tidak ditemukan untuk favorite list.");

                return;
            }

            const component = window.Livewire?.find(wireId);

            if (!component) {
                console.error("Komponen Livewire tidak ditemukan:", wireId);

                return;
            }

            component.call("reorderFavorites", ids);
        },
    });
}

/*
|--------------------------------------------------------------------------
| Livewire
|--------------------------------------------------------------------------
*/

document.addEventListener("livewire:initialized", () => {
    // Tunggu sampai DOM benar-benar siap.
    setTimeout(() => {
        initFavoriteSortable();
    }, 100);

    /*
    | Setiap kali Livewire selesai melakukan morph DOM,
    | pasang kembali Sortable.
    */
    Livewire.hook("morphed", () => {
        setTimeout(() => {
            initFavoriteSortable();
        }, 50);
    });

    Livewire.on("favorite-updated", () => {
        setTimeout(() => {
            initFavoriteSortable();
        }, 50);
    });
});

document.addEventListener("livewire:navigated", () => {
    setTimeout(() => {
        initFavoriteSortable();
    }, 100);
});
