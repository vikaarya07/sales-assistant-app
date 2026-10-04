window.sweetAlert = {
    success(message, title = "Berhasil") {
        return Swal.fire({
            icon: "success",
            title,
            text: message,
            showConfirmButton: false,
            timer: 2000,
        });
    },

    error(message, title = "Terjadi Kesalahan") {
        return Swal.fire({
            icon: "error",
            title,
            text: message,
            showConfirmButton: false,
            timer: 3000,
        });
    },

    warning(message, title = "Peringatan") {
        return Swal.fire({
            icon: "warning",
            title,
            text: message,
            confirmButtonText: "OK",
        });
    },

    info(message, title = "Informasi") {
        return Swal.fire({
            icon: "info",
            title,
            text: message,
            confirmButtonText: "OK",
        });
    },

    confirm(message, title = "Apakah Anda yakin?") {
        return Swal.fire({
            icon: "warning",
            title,
            text: message,
            showCancelButton: true,
            confirmButtonText: "Ya",
            cancelButtonText: "Batal",
            reverseButtons: true,
        });
    },

    toast(message, icon = "success") {
        return Swal.fire({
            toast: true,
            position: "top-end",
            icon,
            title: message,
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
        });
    },
};

// Livewire → SweetAlert2
document.addEventListener("livewire:init", () => {
    Livewire.on("swal", ({ type = "info", title, message }) => {
        const alerts = {
            success: () => window.sweetAlert.success(message, title),
            error: () => window.sweetAlert.error(message, title),
            warning: () => window.sweetAlert.warning(message, title),
            info: () => window.sweetAlert.info(message, title),
        };

        (alerts[type] ?? alerts.info)();
    });

    Livewire.on("toast", ({ type = "success", message }) => {
        window.sweetAlert.toast(message, type);
    });

    Livewire.on(
        "confirm",
        async ({
            title = "Apakah Anda yakin?",
            message = "",
            action = null,
        }) => {
            const result = await window.sweetAlert.confirm(message, title);

            if (result.isConfirmed && action) {
                Livewire.dispatch(action);
            }
        },
    );
});
