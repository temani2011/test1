import axios from "axios";

export default {
    catalogPost(data) {
        console.log(data[0], data[1]);
        return axios.post("/api/catalog", {
            parentId: data[0],
            name: data[1],
        });
    },
    catalogGetAll(){
        return axios.get("/api/catalogs");
    },
    catalogGetById(id){
        return axios.get("/api/catalog/" + id);
    },
    catalogGetBySlug(slug){
        return axios.get("/api/catalog/" + slug);
    },
    catalogUpdate(data){
        console.log(data[0], data[1])
        return axios.put("/api/catalog/" + id, {
            newParentId: data[0],
            name: data[1]
        });
    },
    catalogDeleteById(id){
        return axios.delete("/api/catalog/" + id);
    }
};