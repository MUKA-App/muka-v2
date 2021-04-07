import React from 'react';
import ReactDOM from 'react-dom';
import {PublicRoute, PrivateRoute} from "react-private-public-route";
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

export default function App() {

    return (
        <div className="App">
            <BrowserRouter>
                <Header/>
                <Switch>
                    <PublicRoute exact path={'/register'}><Register/></PublicRoute>
                    <PublicRoute exact path={'/login'}><Login/></PublicRoute>
                    <PublicRoute exact path={'/sponsors'}><Sponsors/></PublicRoute>
                    <PublicRoute exact path={'/home'}><Home/></PublicRoute>
                    <PublicRoute exact path={'/about'}><About/></PublicRoute>
                    <PublicRoute path={'/verify/:token'} redirect={"/home"}><Verify/></PublicRoute>
                    <PublicRoute exact path={'/register/confirm'}><Dialog head={'Registration successful'}
                                                                          body={'Please check your email for confirmation'}/></PublicRoute>
                    <PrivateRoute exact path={'/profiles/create'}><CreateProfile/></PrivateRoute>
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