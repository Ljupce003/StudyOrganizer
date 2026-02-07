import EasyMDE from "easymde";
import "easymde/dist/easymde.min.css";

document.addEventListener("DOMContentLoaded", () => {
    const el = document.querySelector("#note-content");
    if (!el) return;

    new EasyMDE({
        element: el,
        spellChecker: false,
        status: false,
        autofocus: false,
        minHeight: "220px", // ~6–7 lines
    });
});
