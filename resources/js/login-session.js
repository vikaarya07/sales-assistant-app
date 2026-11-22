const sessionCheckMeta = document.querySelector(
    'meta[name="single-session-check"]',
);

if (sessionCheckMeta?.content === "enabled") {
    let checkingSession = false;

    const checkSession = async () => {
        if (checkingSession || document.visibilityState !== "visible") {
            return;
        }

        checkingSession = true;

        try {
            const response = await fetch("/session-status", {
                method: "GET",
                credentials: "same-origin",
                cache: "no-store",
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            });

            if (!response.ok) {
                return;
            }

            const contentType = response.headers.get("content-type");

            if (!contentType?.includes("application/json")) {
                return;
            }

            const result = await response.json();

            if (result.valid === false) {
                window.location.replace("/login?session=replaced");
            }
        } catch {
            // Gangguan jaringan sementara tidak langsung logout.
        } finally {
            checkingSession = false;
        }
    };

    window.setInterval(checkSession, 5000);
}
