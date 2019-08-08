import M from "materialize-css";
import Vue from 'vue';

export default abstract class AbstractDialog extends Vue {
    protected _modalInstance: M.Modal | undefined;
    protected modalOptions: Partial<M.ModalOptions> = {
        onOpenEnd: this.opened,
    };

    public close() {
        this.dialog.close();
        this.$el.remove();
        this.$destroy();
    }

    public open() {
        this.dialog.open();
    }

    protected get dialog(): M.Modal {
        if (!this._modalInstance) {
            this._modalInstance = M.Modal.init(this.$el, this.modalOptions);
        }

        return this._modalInstance;
    }

    protected opened() {
    }
}
