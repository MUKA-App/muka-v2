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