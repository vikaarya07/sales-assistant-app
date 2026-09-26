document.addEventListener("click", (event) => {
    const button = event.target.closest("[data-message-variable]");

    if (!button) {
        return;
    }

    const variable = button.dataset.messageVariable;
    const textarea = document.getElementById("message-content");

    if (!textarea || !variable) {
        return;
    }

    textarea.focus();

    const start = textarea.selectionStart ?? textarea.value.length;
    const end = textarea.selectionEnd ?? textarea.value.length;

    textarea.setRangeText(variable, start, end, "end");

    textarea.dispatchEvent(
        new Event("input", {
            bubbles: true,
        }),
    );

    textarea.focus();
});