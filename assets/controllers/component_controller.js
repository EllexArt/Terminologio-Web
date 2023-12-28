import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        urlComponent: String,
        urlTranslation: String
    }

    static targets = ['components', 'componentsNames', 'style'];
    async addComponent(request) {
        let image = document.getElementById("srcImage");
        let height = image.height
        let width = image.width;
        let offsetX = (request.offsetX / width) * 100;
        let offsetY = (request.offsetY / height) * 100;

        if(isNaN(offsetY) || isNaN(offsetX)) {
            return;
        }

        let tabText = this.calculateValuesOfComponents();

        const response = await fetch(`${this.urlComponentValue}/add/${offsetX}/${offsetY}`, {method: "POST"});
        this.componentsTarget.innerHTML = await response.text();
        await this.updateComponentsToShow();

        let elements = document.getElementsByClassName('componentText');
        for (let i = 0; i < tabText.length; i++) {
            elements[i].setAttribute('value', tabText[i]);
        }
    }

    async deleteComponent(button) {

        let tabText = this.calculateValuesOfComponents();

        const response = await fetch(`${this.urlComponentValue}/delete/${button.params.id}`, {method: "POST"});
        if(!response.ok) {
            location.reload()
        } else {
            this.componentsTarget.innerHTML = await response.text();
        }
        await this.updateComponentsToShow();

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

        languageForm.setAttribute('action', `${this.urlTranslationValue}/save/${languageId}`);

        const response = await fetch(`${this.urlTranslationValue}/get/${languageId}`, {method: "POST"});

        this.componentsNamesTarget.innerHTML = await response.text();
    }

    async getShowingNames() {
        let languageChooser = document.getElementById('languageChooser');
        let languageId = languageChooser.value;

        const response = await fetch(`${this.urlTranslationValue}/${languageId}/components/get`, {method: "POST"});
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

    async updateComponentsToShow() {
        const response_buttons = await fetch(`${this.urlComponentValue}/buttons`, {method: "POST"});
        const style = await fetch(`${this.urlComponentValue}/styles`, {method: "POST"});
        this.componentsNamesTarget.innerHTML = await response_buttons.text();
        this.styleTarget.innerHTML = await style.text();
    }
}