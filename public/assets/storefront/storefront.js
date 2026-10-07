(() => {
    "use strict";
    const root = document.documentElement;
    const themeButton = document.querySelector("[data-theme]");
    const darkPreference = window.matchMedia("(prefers-color-scheme: dark)");
    const isDark = () =>
        root.dataset.theme
            ? root.dataset.theme === "dark"
            : darkPreference.matches;
    const updateThemeLabel = () => {
        const label = isDark() ? "切换浅色模式" : "切换深色模式";
        themeButton.setAttribute("aria-label", label);
        themeButton.setAttribute("title", label);
    };
    try {
        const saved = localStorage.getItem("storefront-theme");
        if (saved === "light" || saved === "dark") root.dataset.theme = saved;
    } catch (_) {
        /* Storage is optional. */
    }
    themeButton.addEventListener("click", () => {
        root.dataset.theme = isDark() ? "light" : "dark";
        try {
            localStorage.setItem("storefront-theme", root.dataset.theme);
        } catch (_) {}
        updateThemeLabel();
    });
    updateThemeLabel();
    if (darkPreference.addEventListener)
        darkPreference.addEventListener("change", updateThemeLabel);

    const grid = document.getElementById("product-grid");
    const cards = [...grid.querySelectorAll(".product-card")];
    const search = document.getElementById("product-search");
    const stock = document.getElementById("in-stock");
    const sort = document.getElementById("product-sort");
    const tabs = [...document.querySelectorAll("[data-category]")];
    let category = "all";
    function filterProducts() {
        const query = search.value.trim().toLocaleLowerCase();
        let visible = 0;
        cards.forEach((card) => {
            card.hidden =
                !(category === "all" || card.dataset.group === category) ||
                !card.dataset.name.toLocaleLowerCase().includes(query) ||
                (stock.checked && card.dataset.stock !== "1");
            if (!card.hidden) visible++;
        });
        [...cards]
            .sort((a, b) => {
                if (
                    sort.value !== "default" &&
                    a.dataset.pending !== b.dataset.pending
                )
                    return (
                        Number(a.dataset.pending) - Number(b.dataset.pending)
                    );
                if (sort.value === "price-asc")
                    return Number(a.dataset.price) - Number(b.dataset.price);
                if (sort.value === "price-desc")
                    return Number(b.dataset.price) - Number(a.dataset.price);
                return Number(a.dataset.index) - Number(b.dataset.index);
            })
            .forEach((card) => grid.appendChild(card));
        document.getElementById("product-count").textContent =
            `${visible} 件商品`;
        document.getElementById("empty-state").hidden = visible !== 0;
        tabs.forEach((tab) => {
            const active = tab.dataset.category === category;
            tab.classList.toggle("active", active);
            tab.setAttribute("aria-pressed", String(active));
        });
    }
    tabs.forEach((tab) =>
        tab.addEventListener("click", () => {
            category = tab.dataset.category;
            filterProducts();
        }),
    );
    search.addEventListener("input", filterProducts);
    stock.addEventListener("change", filterProducts);
    sort.addEventListener("change", filterProducts);
    document.getElementById("reset-filters").addEventListener("click", () => {
        category = "all";
        search.value = "";
        stock.checked = false;
        sort.value = "default";
        filterProducts();
        search.focus();
    });
    document.addEventListener("keydown", (event) => {
        if (
            event.key === "/" &&
            !event.ctrlKey &&
            !event.metaKey &&
            !event.altKey &&
            !event.target.closest("input,textarea,select,[contenteditable]") &&
            !document.getElementById("notice-dialog").open
        ) {
            event.preventDefault();
            search.focus();
        }
    });
    const notice = document.getElementById("notice-dialog");
    document
        .querySelectorAll("[data-notice]")
        .forEach((button) =>
            button.addEventListener("click", () => notice.showModal()),
        );
    document
        .querySelector("[data-close-notice]")
        .addEventListener("click", () => notice.close());
    notice.addEventListener("click", (event) => {
        if (event.target === notice) {
            const box = notice.getBoundingClientRect();
            if (
                event.clientX < box.left ||
                event.clientX > box.right ||
                event.clientY < box.top ||
                event.clientY > box.bottom
            )
                notice.close();
        }
    });
    document.querySelectorAll("img").forEach((img) =>
        img.addEventListener("error", () => {
            img.hidden = true;
            if (img.parentElement.classList.contains("product-icon"))
                img.parentElement.textContent = "✧";
        }),
    );
})();
