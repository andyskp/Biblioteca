// src/components/BookList.jsx
import React, { useEffect, useState } from "react";

export default function BookList() {
  const [books, setBooks] = useState([]);

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

  useEffect(() => {
    fetchBooks();
  }, []);

  return (
    <>
      <h2>📚 Lista de Libros</h2>
      <ul>
        {books.map((book) => (
          <li key={book.id}>
            {book.title} — {book.author} ({book.genre}){" "}
            {book.available ? "✅ Disponible" : "❌ Prestado"}
            <button onClick={() => eliminarLibro(book.id)}>Eliminar</button>
          </li>
        ))}
      </ul>
    </>
  );
}
