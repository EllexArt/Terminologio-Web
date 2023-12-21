import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        url: String
    }

    static targets = ['components'];
    async addComponent(image) {
        console.log(image);
        let heigt = image.target.height;
        let width = image.target.width;
        let offsetX = (image.offsetX / width) * 100;
        let offsetY = (image.offsetY / heigt) * 100;

        const response = await fetch(`${this.urlValue}/${offsetX}/${offsetY}`);
        this.componentsTarget.innerHTML = await response.text();
    }
}