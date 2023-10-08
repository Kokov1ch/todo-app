import React from "react";
import {BrowserRouter as Router, Routes, Route } from "react-router-dom";
import {Login} from "./pages/Login"
import {SignUp} from "./pages/SignUp";


const App: React.FC = () => {
    return (
        <div className="app-container">
            <Router>
                <Routes>
                    <Route path="/" element={<Login />}/>
                    <Route path="/signup" element={<SignUp />}/>
                </Routes>
            </Router>
        </div>
    );
};

export default App;