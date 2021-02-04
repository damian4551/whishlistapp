//variables

//btns
const closeBtns = document.querySelectorAll('.close-btn');
const showOptionsBtn = document.querySelector('.show-options-btn');
const showCatalogFormBtn = document.querySelector('.show-catalog-form');
const showProductFormBtn = document.querySelector('.show-product-form');

//divs to display/hide
const optionsBlock = document.querySelector('.options');
const errorBox = document.querySelector('.error-box');
const catalogForm = document.querySelector('.catalog-form');
const productForm = document.querySelector('.product-form');
const mainContainer = document.querySelector('.main-container');

const products = document.querySelectorAll('.product');
const modals = document.querySelectorAll('.modal');

//functions
function showOptions() {
    let display = getComputedStyle(optionsBlock).display;
    if(display == 'none') {
        optionsBlock.style.display = 'flex';
    } else {
        optionsBlock.style.display = 'none';
    }
}

function showCatalogForm() {
    productForm.style.display = 'none';
    catalogForm.style.display = 'inline';
}

function showProductForm() {
    catalogForm.style.display = 'none';
    productForm.style.display = 'inline';
}

function hideForms() {
    productForm.style.display = 'none';
    catalogForm.style.display = 'none';
    errorBox.style.display = 'none';
    modals.forEach(modal => {
        modal.style.display = "none";
    });
}

function showModal(id) {
    modals.forEach(modal => {
        if(id === modal.id) {
            modal.style.display = "inline";
        }
    });
}

//event listeners
showOptionsBtn.addEventListener('click', showOptions);

showCatalogFormBtn.addEventListener('click', showCatalogForm);

showProductFormBtn.addEventListener('click', showProductForm);

closeBtns.forEach(btn => {
    btn.addEventListener('click', hideForms);
});

//mainContainer.addEventListener('click', hideForms);

//modals
products.forEach(product => {
    product.addEventListener('click', e => showModal(e.target.id));
});