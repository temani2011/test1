import axios from "axios";

export default {
    addUser(data) {
        console.log(data[0], data[1], data[2]);
        return axios.post("/api/user", {
            login: data[0],
            password: data[1],
            roles: data[2],
        });
    },
    getAllUsers(){
        return axios.get("/api/user");
    },
    getUserById(id){
        return axios.get("/api/user/" + id);
    },
    editUser(data){
        console.log(data[0], data[1], data[2], data[3]);
        return axios.put("/api/user/" + data[0], {
            login: data[1],
            password: data[2],
            roles: data[3],
        });
    },
    deleteUser(id){
        return axios.delete("/api/news/" + id );
    }
};