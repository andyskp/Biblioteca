// src/components/BookForm.jsx
import React, { useState, useEffect } from "react";
import "./BookForm.css";

export default function BookForm({ onBookAdded, onBookUpdated, selectedBook }) {
  const [form, setForm] = useState({
    title: "",
    author: "",
    genre: "",
    available: true,
  });

  useEffect(() => {
    if (selectedBook) {
      setForm(selectedBook);
    } else {
      setForm({
        title: "",
        author: "",
        genre: "",
        available: true,
      });
    }
  }, [selectedBook]);

  const handleChange = (e) => {
    const { name, value, type, checked } = e.target;
    setForm({
      ...form,
      [name]: type === "checkbox" ? checked : value,
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const endpoint = selectedBook
      ? `http://localhost:8000/books/${selectedBook.id}`
      : "http://localhost:8000/books";

    const method = selectedBook ? "PUT" : "POST";

    try {
      const res = await fetch(endpoint, {
        method,
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": csrfToken,
        },
        body: JSON.stringify(form),
      });

      if (!res.ok) throw new Error("Error al guardar libro");

      const data = await res.json();

      if (selectedBook) {
        onBookUpdated(data);
      } else {
        onBookAdded(data);
      }

      setForm({ title: "", author: "", genre: "", available: true });
    } catch (err) {
      console.error("❌ Error al guardar:", err.message);
    }
  };

  return (
    <form className="book-form" onSubmit={handleSubmit}>
      <h3 className="book-form-title">{selectedBook ? "✏️ Editar libro" : "➕ Agregar nuevo libro"}</h3>
      <input
        className="book-form-input"
        name="title"
        placeholder="Título"
        value={form.title}
        onChange={handleChange}
        required
      />
      <input
        className="book-form-input"
        name="author"
        placeholder="Autor"
        value={form.author}
        onChange={handleChange}
        required
      />
      <input
        className="book-form-input"
        name="genre"
        placeholder="Género"
        value={form.genre}
        onChange={handleChange}
        required
      />
      <label className="book-form-checkbox-label">
        <input
          className="book-form-checkbox"
          type="checkbox"
          name="available"
          checked={form.available}
          onChange={handleChange}
        />
        Disponible
      </label>
      <button className="book-form-button" type="submit">Guardar</button>
    </form>
  );
}
