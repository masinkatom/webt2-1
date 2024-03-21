$(document).ready(function () {
    let btnAdd = document.getElementById("record-add");
    let btnEdit = document.getElementById("record-edit");
    let btnRemove = document.getElementById("record-remove");

    btnAdd.addEventListener("click", showForm);
    btnEdit.addEventListener("click", showForm);
    btnRemove.addEventListener("click", showForm);

    let oldFormId = "";

    function showForm(e) {
        changeShowedForm("form-" + e.target.id);
    }

    function changeShowedForm(formId) {
        let newForm = document.getElementById(formId).parentNode;

        var forms = document.querySelectorAll('div.form-outline');
        forms.forEach((form) => {
            form.classList.add('hidden');
        });
        if (formId != oldFormId) {
            newForm.classList.remove("hidden");
            oldFormId = formId;
        }
        else {
            oldFormId = "";
        }
    }
});