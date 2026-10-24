document.addEventListener("DOMContentLoaded", () => {
    const usuariosTable = document.getElementById("usuarios-table");
    const cursosTable = document.getElementById("cursos-table");
    const usuariosBtn = document.getElementById("usuarios-btn");
    const cursosBtn = document.getElementById("cursos-btn");

    usuariosBtn.addEventListener("click", () => {
        usuariosTable.classList.remove("hidden");
        cursosTable.classList.add("hidden");
    });

    cursosBtn.addEventListener("click", () => {
        cursosTable.classList.remove("hidden");
        usuariosTable.classList.add("hidden");
    });
});
