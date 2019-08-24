import App from "@src/Vue/App";
import Vue from 'vue';
import {compileToFunctions} from "vue-template-compiler";

export function clickOn(el: Element | null): boolean {
    if (!el) {
        return false;
    }

    let event = new MouseEvent('click');
    return el.dispatchEvent(event);
}

export function containing(elements: NodeListOf<Element>, pattern: string): Element[] {
    return Array.prototype.filter.call(elements, function (element: Element) {
        return RegExp(pattern).test(element.textContent || '');
    });
}

export function prepareComponent<T extends Vue>(Component: { new(options: any): T}, options: any = {}): T {
    Object.defineProperty(HTMLElement.prototype, 'offsetParent', {
        get() { return this.parentNode; },
    });

    let app = new App({
        render: compileToFunctions(
`<div id="riki-community">
  <div ref=overlayContainer></div>
</div>`
        ).render,
    });
    app.$mount();

    let component = new Component(Object.assign({
        parent: app,
    }, options));
    component.$mount();
    app.$el.appendChild(component.$el);

    document.body.appendChild(app.$el);

    return component;
}
