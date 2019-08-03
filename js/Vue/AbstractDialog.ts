import M from "materialize-css";
import Vue from 'vue';

export default abstract class AbstractDialog extends Vue {
    protected _modalInstance: M.Modal | undefined;

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
            this._modalInstance = M.Modal.init(this.$el, {
                onOpenEnd: this.opened,
            });
        }

        return this._modalInstance;
    }

    protected opened() {
    }
}
