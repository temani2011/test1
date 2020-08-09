import axios from "axios";

export default {
    postNews(data) {
        console.log(data[0], data[1], data[2], data[3]);
        return axios.post("/api/news", {
            coverText: data[0],
            coverImage: data[1],
            title: data[2],
            text: data[3]
        });
    },
    getAllNews(){
        return axios.get("/api/news");
    },
    getNews(id){
        return axios.get("/api/news/" + id);
    },
    putNews(data){
        console.log(data[0], data[1], data[2], data[3], data[4]);
        return axios.put("/api/news/" + data[0], {
            coverText: data[1],
            coverImage: data[2],
            title: data[3],
            text: data[4]
        });
    },
    deleteNews(id){
        return axios.delete("/api/news/" + id );
    }
};