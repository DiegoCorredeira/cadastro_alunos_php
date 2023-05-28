function mostraForm(){
    let form = document.getElementById("cadastro-form");
    let addButton = document.getElementById("adicionar");
    let editarButton = document.getElementById("editar");

    if(form){

        form.style.display = "block";
    }
    addButton.style.display = "none";
    editarButton.style.display = "none";

}
