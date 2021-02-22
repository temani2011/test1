import axios from "axios";

export default {
    documentPost(data) {
        console.log(data);
        return axios.post("/api/document", data,
            {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }
        );
    },
    documentGetByName(name){
        return axios.get("/api/document/name=" + name);
    },
    documentGetById(id){
        return axios.get("/api/document/id=" + id);
    },
    documentGetAllFromCatalog(slug){
        return axios.get("/document/catalogId=" + id);
    },
    documentUpdate(data){
        console.log(data[0], data[1], data[2])
        return axios.put("/api/document/" + data[0], {
            newCatalogId: data[1],
            fileName: data[2]
        });
    },
    documentDeleteById(id){
        return axios.delete("/api/document/" + id);
    }
};