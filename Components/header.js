class MyHeader extends HTMLElement {
    connectedCallback() {
        this.innerHTML = `
            <header class="header">
                <div class="logo-img">
                    <img src="https://img.icons8.com/?size=100&id=tdYiJO7G7zvI&format=png&color=000000">
                </div>
                <div class="title">
                    <p>FitPlan</p>
                </div>
            </header>
        `;
    }
}
customElements.define('my-header', MyHeader);
