import axios from "axios";

export async function store(url, inserts) {
    return axios
        .post(url, inserts)
        .then(async (response) => {
            return response.data;
        })
        .catch((error) => {
            console.log(error);
        });
}

