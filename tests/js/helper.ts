export function clickOn(el: Element|null): boolean {
    if (!el) {
        return false;
    }

    let event = new MouseEvent('click');
    return el.dispatchEvent(event);
}
