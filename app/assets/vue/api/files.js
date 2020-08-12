import axios from "axios";

export default {
    loadFile(data) {
        console.log(data);
        return axios.post("/api/file", data,
            {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            },
        );
    },
    deleteFile(data) {
        console.log(data[0], data[1]);
        return axios.delete("/api/file", { data: {
            fileName: data[0],
            componentName: data[1],
        }});
    },
};