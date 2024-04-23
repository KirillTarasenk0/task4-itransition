/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

const checkAll = document.querySelector('.checkAll');
const checkboxes = document.querySelectorAll('.checkbox-element');
checkAll.addEventListener('change', () => {
    for (let i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = checkAll.checked;
    }
});
