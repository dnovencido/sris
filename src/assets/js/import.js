const btnDelete = document.getElementsByClassName("btn-delete");

for (let i=0; i<btnDelete.length; i++) {
    btnDelete[i].addEventListener("click", function(e) {
        e.preventDefault();
        var result = confirm("Do you want to delete this item?");
        if(result) {
            var btn = this;
            var id = this.getAttribute("data-id");
            url = this.getAttribute("data-url");
            fetch(`${url}/${id}`, {
                method:'DELETE',
                headers: {
                    "Content-Type": "application/json",
                },
            }).then(function(response) {
                return response.json(); 
            }).then(function(data) {
                if(data.deleted) {
                    const row = btn.closest('tr');
                    if (row) {
                        row.remove();
                    } else {
                        window.location.href = '/imports/';
                    }
                }
            }).catch(function(e) { 
                console.log(e)
            })        
        }
    });
}