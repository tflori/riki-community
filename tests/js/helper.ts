export function clickOn(el: Element|null): boolean {
    if (!el) {
        return false;
    }

    let event = new MouseEvent('click');
    return el.dispatchEvent(event);
}

export function containing(elements: NodeListOf<Element>, pattern: string): Element[] {
    return Array.prototype.filter.call(elements, function(element: Element) {
        return RegExp(pattern).test(element.textContent || '');
    });
}
