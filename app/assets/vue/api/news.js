import axios from "axios";

export default {
    postNews(data) {
        console.log(data);
        return axios.post("/api/news", data,
            {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }
        );
    },
    getAllNews(){
        return axios.get("/api/news");
    },
    getNews(id){
        return axios.get("/api/news/" + id);
    },
    putNews(data){
        console.log(data[0], data[1]);
        return axios.post("/api/news/" + data[0], data[1],
            {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }
        );
    },
    deleteNews(id){
        return axios.delete("/api/news/" + id );
    }
};