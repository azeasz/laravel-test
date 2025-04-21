// src/components/Header.js
import React from 'react';

const Header = () => (
  <header className="p-3">
    <div className="container d-flex justify-content-between align-items-center">
      <div className="logo">
        <img src="/storage/icon/FOBI.png" alt="Fobi Logo" />
      </div>
      <nav>
        <div className="links">
          <ul className="nav">
            <p><a href="#">Jelajahi</a></p>
            <p><a href="#">Eksplorasi Saya</a></p>
            <p><a href="#">Bantu Ident</a></p>
            <p><a href="#">Komunitas</a></p>
          </ul>
        </div>
      </nav>
    </div>
  </header>
);

export default Header;
