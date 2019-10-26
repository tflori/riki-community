// Type definitions for Google Recaptcha 2.0
// Project: https://www.google.com/recaptcha
// Definitions by: Kristof Mattei <http://kristofmattei.be>
//                 Martin Costello <https://martincostello.com/>
//                 Ruslan Arkhipau <https://github.com/DethAriel>
//                 Rafael Tavares <https://github.com/rafaeltavares>
// Definitions: https://github.com/DefinitelyTyped/DefinitelyTyped

declare var grecaptcha: ReCaptchaV3.ReCaptcha;

declare namespace ReCaptchaV3 {
    interface ReCaptcha {
        /**
         * Programatically invoke the reCAPTCHA check. Used if the invisible reCAPTCHA is on a div instead of a button.
         * @param opt_widget_id Optional widget ID, defaults to the first widget created if unspecified.
         */
        execute(sitekey: string, parameters?: {action?: string}): Promise<string>;

        /**
         * Wait for reCAPTCHA to get ready.
         * @param callback callback to execute when reCAPTCHA gets ready
         */
        ready(callback: () => void): void;
    }
}
