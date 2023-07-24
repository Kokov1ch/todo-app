import React from "react";
import {BrowserRouter as Router, Routes, Route } from "react-router-dom";
import {Login} from "./components/Login/";


const App: React.FC = () => {
    return (
        <div className="app-container">
            <Router>
                <Routes>
                    <Route path="/" element={<Login />}/>
                </Routes>
            </Router>
        </div>
    );
};

export default App;