import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        url: String
    }

    static targets = ['components', 'components_button'];
    async addComponent(image) {
        let heigt = image.target.height;
        let width = image.target.width;
        let offsetX = (image.offsetX / width) * 100;
        let offsetY = (image.offsetY / heigt) * 100;

        let tabText = this.calculateValuesOfComponents();

        const response = await fetch(`${this.urlValue}/add/${offsetX}/${offsetY}`);
        const response_buttons = await fetch(`${this.urlValue}/buttons`);
        this.componentsTarget.innerHTML = await response.text();
        this.components_buttonTarget.innerHTML = await response_buttons.text();

        let elements = document.getElementsByClassName('componentText');
        for (let i = 0; i < tabText.length; i++) {
            elements[i].setAttribute('value', tabText[i]);
        }
    }

    async deleteComponent(button) {

        let tabText = this.calculateValuesOfComponents();

        const response = await fetch(`${this.urlValue}/delete/${button.params.id}`);
        const response_buttons = await fetch(`${this.urlValue}/buttons`);
        this.componentsTarget.innerHTML = await response.text();
        this.components_buttonTarget.innerHTML = await response_buttons.text();

        let elements = document.getElementsByClassName('componentText');
        tabText.splice(button.params.number, 1);
        for (let i = 0; i < tabText.length; i++) {
            elements[i].setAttribute('value', tabText[i]);
        }
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