// src/components/BookList.jsx
import React, { useEffect, useState } from "react";
import BookForm from "./BookForm";
import "./BookList.css";

export default function BookList() {
  const [books, setBooks] = useState([]);
  const [selectedBook, setSelectedBook] = useState(null);

  const fetchBooks = async () => {
    try {
      const res = await fetch("http://localhost:8000/books");
      const data = await res.json();
      setBooks(data);
    } catch (err) {
      console.error("❌ Error cargando libros:", err.message);
    }
  };

  const eliminarLibro = async (id) => {
    try {
      await fetch(`http://localhost:8000/books/${id}`, {
        method: "DELETE",
      });
      setBooks(books.filter((b) => b.id !== id));
    } catch (err) {
      console.error("❌ Error al eliminar:", err.message);
    }
  };

  const handleBookAdded = (nuevoLibro) => {
    setBooks([...books, nuevoLibro]);
  };

  const handleBookUpdated = (libroActualizado) => {
    setBooks(
      books.map((book) =>
        book.id === libroActualizado.id ? libroActualizado : book
      )
    );
    setSelectedBook(null);
  };

  useEffect(() => {
    fetchBooks();
  }, []);

  return (
    <div className="book-list-container">
      <h2 className="book-list-title">📚 Biblioteca Digital</h2>
      <BookForm
        onBookAdded={handleBookAdded}
        onBookUpdated={handleBookUpdated}
        selectedBook={selectedBook}
      />
      <ul className="book-list">
        {books.map((book) => (
          <li className="book-list-item" key={book.id}>
            <span>
              {book.title} — {book.author} ({book.genre}){" "}
              {book.available ? "✅ Disponible" : "❌ Prestado"}
            </span>
            <div className="book-list-actions">
              <button onClick={() => setSelectedBook(book)}>Editar</button>
              <button onClick={() => eliminarLibro(book.id)}>Eliminar</button>
            </div>
          </li>
        ))}
      </ul>
    </div>
  );
}
