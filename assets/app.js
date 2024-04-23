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

const blockForm = document.getElementById('blockForm');
const unblockForm = document.getElementById('unblockForm');
const deleteForm = document.getElementById('deleteForm');
const actionInputBlock = document.getElementById('actionInputBlock');
const actionInputUnblock = document.getElementById('actionInputUnblock');
const actionInputDelete = document.getElementById('actionInputDelete');
const checkboxValues = document.querySelectorAll('.checkbox-element');

const otherValues = [];
checkboxValues.forEach((checkbox) => {
    if (checkbox.checked) {
        otherValues.push(checkbox.value);
    }
});

deleteForm.addEventListener('submit', () => {
    actionInputDelete.value = JSON.stringify(otherValues);
});

blockForm.addEventListener('submit', () => {
    actionInputBlock.value = JSON.stringify(otherValues);
});

unblockForm.addEventListener('submit', () => {
    actionInputUnblock.value = JSON.stringify(otherValues);
});