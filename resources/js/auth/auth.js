
export const isLogin = () => {
    if (localStorage.getItem('auth')) return true;
    return false;
}