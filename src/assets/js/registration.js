const btnDelete = document.getElementsByClassName("btn-delete");

for (let i=0; i<btnDelete.length; i++) {
    btnDelete[i].addEventListener("click", function(e) {
        e.preventDefault();
        var result = confirm("Do you want to delete this dish?");
        if(result) {
            var btn = this;
            var id = this.getAttribute("data-id");
            fetch(`delete.php?id=${id}`, {
                method:'DELETE',
                headers: {
                    "Content-Type": "application/json",
                },
            }).then(function(response) {
                return response.json(); 
            }).then(function(data) {
                if(data.deleted)
                    btn.closest('tr').remove();
            }).catch(function(e) { 
                console.log(e)
            })        
        }
    });
}