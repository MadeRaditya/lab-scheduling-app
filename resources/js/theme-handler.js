window.addEventListener("themeChanged", (event) => {
    window.handleThemeChange(event?.detail?.theme);
});

window.handleThemeChange = (newTheme) => {
    document.documentElement.classList.toggle("dark", newTheme === "dark");
    localStorage.setItem("theme", newTheme);
};
