import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        url: String
    }

    static targets = ['components', 'componentsNames'];
    async addComponent(image) {
        let height = image.target.height;
        let width = image.target.width;
        let offsetX = (image.offsetX / width) * 100;
        let offsetY = (image.offsetY / height) * 100;

        let tabText = this.calculateValuesOfComponents();

        const response = await fetch(`${this.urlValue}/add/${offsetX}/${offsetY}`, {method: "POST"});
        const response_buttons = await fetch(`${this.urlValue}/buttons`, {method: "POST"});
        this.componentsTarget.innerHTML = await response.text();
        this.componentsNamesTarget.innerHTML = await response_buttons.text();

        let elements = document.getElementsByClassName('componentText');
        for (let i = 0; i < tabText.length; i++) {
            elements[i].setAttribute('value', tabText[i]);
        }
    }

    async deleteComponent(button) {

        let tabText = this.calculateValuesOfComponents();

        const response = await fetch(`${this.urlValue}/delete/${button.params.id}`, {method: "POST"});
        const response_buttons = await fetch(`${this.urlValue}/buttons`, {method: "POST"});
        this.componentsTarget.innerHTML = await response.text();
        this.componentsNamesTarget.innerHTML = await response_buttons.text();

        let elements = document.getElementsByClassName('componentText');
        tabText.splice(button.params.number, 1);
        for (let i = 0; i < tabText.length; i++) {
            elements[i].setAttribute('value', tabText[i]);
        }
    }

    async getTranslation() {
        let languageForm = document.getElementById('translationForm');
        let languageChooser = document.getElementById('languageChooser');
        let languageId = languageChooser.value;

        languageForm.setAttribute('action', `${this.urlValue}/save/${languageId}`);

        const response = await fetch(`${this.urlValue}/get/${languageId}`, {method: "POST"});

        this.componentsNamesTarget.innerHTML = await response.text();
    }

    async getShowingNames() {
        let languageChooser = document.getElementById('languageChooser');
        let languageId = languageChooser.value;

        const response = await fetch(`${this.urlValue}/${languageId}/components/get`, {method: "POST"});
        this.componentsNamesTarget.innerHTML = await response.text();
    }

    calculateValuesOfComponents() {
        let tabText = [];
        let elements = document.getElementsByClassName('componentText');
        for (let i = 0; i < elements.length; i++) {
            tabText.push(elements[i].value);
        }
        return tabText;
    }
}