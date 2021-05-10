import axios from "axios";

export const login = () => {
    localStorage.setItem('auth', 'true');
};

export const logout = () => {
    localStorage.removeItem('auth');
};

export const isLogin = () => {
    return !!localStorage.getItem('auth');
};

export const hasProfile = async function getProfile() {
    let found = false;
    axios.get(process.env.MIX_APP_BASE_URL + "/api/profile")
        .then(response => {
            if (response.status === 200) {
                found = true;
            } else {
                console.log(response);
            }
        })
        .catch(error => {
            if (error.response.status === 404 || error.response.status === 401) {
                found = false;
            } else {
                console.log(error.response);
            }
        });
    return found;
}
