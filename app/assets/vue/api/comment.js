import axios from "axios";

export default {
    postComment(data) {
        console.log(data[0], data[1], data[2]);
        return axios.post("/api/comment", {
            pid: data[0],
            text: data[1],
            author: data[2]
        });
    },
    getAllComments(pid){
        return axios.get("/api/comment/" + pid);
    },
    getComment(id){
        return axios.get("/api/comment/slug=" + id);
    }
};