import moxios from "moxios";

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

export function respondWith(status: number = 200, response: any): Promise<any> {
    return new Promise((resolve, reject) => {
        moxios.wait(() => {
            let request = moxios.requests.mostRecent();

            request.respondWith({
                status: status,
                response: response
            }).then(resolve, reject);
        });
    });
}
