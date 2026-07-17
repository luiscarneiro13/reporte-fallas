import { createRoot } from "@wordpress/element";
import { useEffect } from "@wordpress/element";
import { registerPlugin } from "@wordpress/plugins";
import { useDispatch, useSelect } from "@wordpress/data";
import { store as blockEditorStore } from "@wordpress/block-editor";
import { parse } from "@wordpress/blocks";
import { __ } from "@wordpress/i18n";
import "./index.css";
import "./store/index.js";
import { STORE_NAME } from "./store/index.js";
import { PatternLibraryModal } from "./components/PatternLibraryModal.js";

function render(component, node) {
  createRoot(node).render(component);
}

function MainButton() {
  const { openModal } = useDispatch(STORE_NAME);

  return (
    <div
      role="button"
      onClick={() => openModal()}
      className="components-button is-secondary sydney-pattern-library-toolbar-btn"
      style={{ marginLeft: "8px" }}
    >
      <svg
        width="16"
        height="16"
        viewBox="0 0 35 36"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path
          d="M17.7169 0H34.3047V18.119L17.7169 0ZM0.161307 10.6334H0C0 7.00073 1.00369 4.32034 3.01107 2.59221C5.01846 0.86407 8.11017 0 12.2863 0L34.3047 26.2925C34.3047 29.7841 33.3459 32.2793 31.428 33.7782C29.564 35.2417 26.5171 35.9735 22.2873 35.9735L0.161307 10.6334ZM0.0806536 17.7752L16.0501 36H0.0806536V17.7752Z"
          fill="currentColor"
        />
      </svg>
      <span className="sydney-pattern-library-toolbar-btn-text">
        {__("Pattern Library", "sydney")}
      </span>
    </div>
  );
}

function Modal() {
  const { closeModal } = useDispatch(STORE_NAME);
  const { insertBlocks } = useDispatch(blockEditorStore);
  const isOpen = useSelect((select) => select(STORE_NAME).isModalOpen());

  function handleInsert(pattern) {
    const blocks = parse(pattern.content);
    insertBlocks(blocks);
    closeModal();
  }

  return (
    isOpen && <PatternLibraryModal onClose={closeModal} onInsert={handleInsert} />
  );
}

const LibraryButton = () => {
  useEffect(() => {
    const btnId = "sydney-pattern-library-btn";
    const mdlId = "sydney-pattern-library-modal";
    const toolbar = ".editor-document-tools";
    if (document.getElementById(btnId)) return;

    setTimeout(() => {
      if (document.getElementById(btnId)) return;
      const btnWrap = document.createElement("div");
      const btn = Object.assign(btnWrap, { id: btnId });
      document.querySelector(toolbar)?.after(btn);
      render(<MainButton />, btn);

      if (document.getElementById(mdlId)) return;
      const modalWrap = document.createElement("div");
      const modal = Object.assign(modalWrap, { id: mdlId });
      document.body.append(modal);
      render(<Modal />, modal);
    }, 300);
  }, []);
  return null;
};

registerPlugin("sydney-pattern-library", {
  render: () => <LibraryButton />,
});
