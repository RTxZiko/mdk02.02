class MyFooter extends HTMLElement {
    connectedCallback() {
        this.innerHTML = `
            <footer class="footer">
                <p>© 2026. FitPlan: фитнес онлайн</p>
            </footer>
        `;
    }
}

customElements.define('my-footer', MyFooter);
