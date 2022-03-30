import { FormElement } from "../FormElement";
const $ = jQuery;
export class DateFormElement extends FormElement {
    constructor(options, formBuilder, external) {
        super(options, formBuilder, external);
        if (this.formBuilder.admin) {
            this.languages = [
                "en-GB",
                "ar",
                "az",
                "bg",
                "bs",
                "ca",
                "cs",
                "cy",
                "da",
                "de",
                "el",
                "es",
                "et",
                "eu",
                "fa",
                "fi",
                "fo",
                "fr",
                "gl",
                "he",
                "hr",
                "hu",
                "hy",
                "id",
                "is",
                "it",
                "ja",
                "ka",
                "kh",
                "kk",
                "kr",
                "lt",
                "lv",
                "mk",
                "ms",
                "nb",
                "nl",
                "nl-BE",
                "no",
                "pl",
                "pt-BR",
                "pt",
                "ro",
                "rs",
                "rs-latin",
                "ru",
                "sk",
                "sl",
                "sq",
                "sr",
                "sr-latin",
                "sv",
                "sw",
                "th",
                "tr",
                "uk",
                "vi",
                "zh-CN",
                "zh-TW",
            ];
        }
        this.db_type = "varchar(50)";
        this.language = options.language;
    }
    setDomElements() {
        super.setDomElements();
        this.inputs.date = $(this.domElements.main).find("input")[0];
    }
    setEventHandlers() {
        super.setEventHandlers();
        $(this.inputs.date).on("change", (e) => {
            this.setValue(e.target.value, false);
            this.triggerChanged();
        });
    }
    getSchema() {
        const schema = super.getSchema();
        schema.language = this.language;
        return schema;
    }
    getDataForTemplate() {
        const data = super.getDataForTemplate();
        if (this.formBuilder.admin)
            data.languages = this.languages;
        data.language = this.language;
        return data;
    }
}
//# sourceMappingURL=DateFormElement.js.map