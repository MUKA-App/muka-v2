import React from 'react';
import ReactDOM from 'react-dom';
import {Redirect, BrowserRouter, Route, Switch} from 'react-router-dom';
import '../../sass/app.scss';

import Header from './Header';
import Home from './Home';
import About from './About';
import Sponsors from './Sponsors';
import Login from './Login';
import Register from './Register';
import Footer from './Footer';
import Dialog from "./Dialog";
import CreateProfile from './CreateProfile';
import Verify from "./Verify";
import PublicRoute from "./PublicRoute";
import PrivateRoute from "./PrivateRoute";
import Profile from "./Profile";
import Dashboard from "./Dashboard";

export default function App() {

    return (
        <div className="App">
            <BrowserRouter>
                <Header/>
                <Switch>
                    <PublicRoute restricted={true} exact path={'/register'}><Register/></PublicRoute>
                    <PublicRoute restricted={true} exact path={'/login'}><Login/></PublicRoute>
                    <PublicRoute restricted={true} exact path={'/sponsors'}><Sponsors/></PublicRoute>
                    <PublicRoute restricted exact path={'/home'}><Home/></PublicRoute>
                    <PublicRoute restricted={true} exact path={'/about'}><About/></PublicRoute>
                    <PublicRoute restricted={true} path={'/verify/:token'} redirect={"/home"}><Verify/></PublicRoute>
                    <PublicRoute restricted={true} exact path={'/register/confirm'}><Dialog head={'Registration successful'}
                                                                          body={'Please check your email for confirmation'}/></PublicRoute>
                    <PrivateRoute exact path={'/profiles/create'}><CreateProfile/></PrivateRoute>
                    <PrivateRoute exact path={'/profile'}><Profile/></PrivateRoute>
                    <PrivateRoute exact path={'/dashboard'}><Dashboard/></PrivateRoute>
                    {/*<PrivateRoute exact path={'/logout'}><Logout/></PrivateRoute>*/}
                    <Redirect to={"/home"}/>
                </Switch>
            </BrowserRouter>
            <Footer/>
        </div>
    )
}

// DOM element
if (document.getElementById('user')) {
    ReactDOM.render(<App/>, document.getElementById('user'));
}