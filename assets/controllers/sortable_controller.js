import { Controller } from "@hotwired/stimulus";
import Sortable from "sortablejs";

export default class extends Controller {
    static values = { group: String };

    connect() {
        this.sortable = new Sortable(this.element, {
            group: {
                name: this.groupValue || "shared", 
                pull: true,  
                put: true 
            },
            sort: false ,
            animation: 150,
            ghostClass: "sortable-ghost",
            onAdd: this.onAdd.bind(this),
        });
    }
    onAdd(event) {
      
      console.log(`Déplacer l'élément ${event.item.dataset.id} de ${event.from.dataset.group} à ${event.to.dataset.group}`);
    
        const jsonData = {
            document_id: event.item.dataset.id,
            status_id: event.to.dataset.group,
        };

        fetch('/document/api_update_status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(jsonData)
        })
        .then(response => response.json())
        .then(data => console.log("Réponse du serveur :", data))
        .catch(error => console.error("Erreur :", error));
    }
   
}
