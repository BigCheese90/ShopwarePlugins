import { LanguageSupport } from '@codemirror/language';
import { LintSource } from '@codemirror/lint';
import { EditorSelection, EditorState, Extension, SelectionRange, StateField, Text } from '@codemirror/state';
import { EditorView, ViewUpdate } from '@codemirror/view';
import { PropType, Ref, ShallowRef, WritableComputedRef } from 'vue-demi';
import { StyleSpec } from 'style-mod';
/** CodeMirror Component */
declare const _default: import('vue').DefineComponent<import('vue').ExtractPropTypes<{
    /** Model value */
    modelValue: {
        type: PropType<string | Text>;
        default: string;
    };
    /**
     * Theme
     *
     * @see {@link https://codemirror.net/docs/ref/#view.EditorView^theme}
     */
    theme: {
        type: PropType<Record<string, StyleSpec>>;
        default: () => {};
    };
    /** Dark Mode */
    dark: {
        type: BooleanConstructor;
        default: boolean;
    };
    /**
     * Use Basic Setup
     *
     * @see {@link https://codemirror.net/docs/ref/#codemirror.basicSetup}
     */
    basic: {
        type: BooleanConstructor;
        default: boolean;
    };
    /**
     * Use Minimal Setup (The basic setting has priority.)
     *
     * @see {@link https://codemirror.net/docs/ref/#codemirror.minimalSetup}
     */
    minimal: {
        type: BooleanConstructor;
        default: boolean;
    };
    /**
     * Placeholder
     *
     * @see {@link https://codemirror.net/docs/ref/#view.placeholder}
     */
    placeholder: {
        type: PropType<string | HTMLElement>;
        default: undefined;
    };
    /**
     * Line wrapping
     *
     * An extension that enables line wrapping in the editor (by setting CSS white-space to pre-wrap in the content).
     *
     * @see {@link https://codemirror.net/docs/ref/#view.EditorView%5ElineWrapping}
     */
    wrap: {
        type: BooleanConstructor;
        default: boolean;
    };
    /**
     * Allow tab key indent.
     *
     * @see {@link https://codemirror.net/examples/tab/}
     */
    tab: {
        type: BooleanConstructor;
        default: boolean;
    };
    /**
     * Tab character
     */
    indentUnit: {
        type: StringConstructor;
        default: undefined;
    };
    /**
     * Allow Multiple Selection.
     *
     * @see {@link https://codemirror.net/docs/ref/#state.EditorState^allowMultipleSelections}
     */
    allowMultipleSelections: {
        type: BooleanConstructor;
        default: boolean;
    };
    /**
     * Tab size
     *
     * @see {@link https://codemirror.net/docs/ref/#state.EditorState^tabSize}
     */
    tabSize: {
        type: NumberConstructor;
        default: undefined;
    };
    /**
     * Set line break (separetor) char.
     *
     * @see {@link https://codemirror.net/docs/ref/#state.EditorState^lineSeparator}
     */
    lineSeparator: {
        type: StringConstructor;
        default: undefined;
    };
    /**
     * Readonly
     *
     * @see {@link https://codemirror.net/docs/ref/#state.EditorState^readOnly}
     */
    readonly: {
        type: BooleanConstructor;
        default: boolean;
    };
    /**
     * Disable input.
     *
     * This is the reversed value of the CodeMirror editable.
     * Similar to `readonly`, but setting this value to true disables dragging.
     *
     * @see {@link https://codemirror.net/docs/ref/#view.EditorView^editable}
     */
    disabled: {
        type: BooleanConstructor;
        default: boolean;
    };
    /**
     * Additional Extension
     *
     * @see {@link https://codemirror.net/docs/ref/#state.Extension}
     */
    extensions: {
        type: PropType<Extension[]>;
        default: () => never[];
    };
    /**
     * Language Phreses
     *
     * @see {@link https://codemirror.net/examples/translate/}
     */
    phrases: {
        type: PropType<Record<string, string>>;
        default: () => undefined;
    };
    /**
     * CodeMirror Language
     *
     * @see {@link https://codemirror.net/docs/ref/#language}
     */
    lang: {
        type: PropType<LanguageSupport>;
        default: () => undefined;
    };
    /**
     * CodeMirror Linter
     *
     * @see {@link https://codemirror.net/docs/ref/#lint.linter}
     */
    linter: {
        type: PropType<LintSource | any>;
        default: undefined;
    };
    /**
     * Linter Config
     *
     * @see {@link https://codemirror.net/docs/ref/#lint.linter^config}
     */
    linterConfig: {
        type: ObjectConstructor;
        default: () => {};
    };
    /**
     * Forces any linters configured to run when the editor is idle to run right away.
     *
     * @see {@link https://codemirror.net/docs/ref/#lint.forceLinting}
     */
    forceLinting: {
        type: BooleanConstructor;
        default: boolean;
    };
    /**
     * Show Linter Gutter
     *
     * An area to 🔴 the lines with errors will be displayed.
     * This feature is not enabled if `linter` is not specified.
     *
     * @see {@link https://codemirror.net/docs/ref/#lint.lintGutter}
     */
    gutter: {
        type: BooleanConstructor;
        default: boolean;
    };
    /**
     * Gutter Config
     *
     * @see {@link https://codemirror.net/docs/ref/#lint.lintGutter^config}
     */
    gutterConfig: {
        type: ObjectConstructor;
        default: () => undefined;
    };
    /**
     * Using tag
     */
    tag: {
        type: StringConstructor;
        default: string;
    };
    /**
     * Allows an external update to scroll the form.
     * @see {@link https://codemirror.net/docs/ref/#state.TransactionSpec.scrollIntoView}
     */
    scrollIntoView: {
        type: BooleanConstructor;
        default: boolean;
    };
}>, {
    editor: Ref<HTMLElement | undefined, HTMLElement | undefined>;
    view: ShallowRef<EditorView>;
    cursor: WritableComputedRef<number, number>;
    selection: WritableComputedRef<EditorSelection, EditorSelection>;
    focus: WritableComputedRef<boolean, boolean>;
    length: Ref<number, number>;
    json: WritableComputedRef<Record<string, StateField<any>>, Record<string, StateField<any>>>;
    diagnosticCount: Ref<number, number>;
    dom: HTMLElement;
    lint: () => void;
    forceReconfigure: () => void;
    getRange: (from?: number, to?: number) => string | undefined;
    getLine: (number: number) => string;
    lineCount: () => number;
    getCursor: () => number;
    listSelections: () => readonly SelectionRange[];
    getSelection: () => string;
    getSelections: () => string[];
    somethingSelected: () => boolean;
    replaceRange: (replacement: string | Text, from: number, to: number) => void;
    replaceSelection: (replacement: string | Text) => void;
    setCursor: (position: number) => void;
    setSelection: (anchor: number, head?: number) => void;
    setSelections: (ranges: readonly SelectionRange[], primary?: number) => void;
    extendSelectionsBy: (f: any) => void;
}, {}, {}, {}, import('vue').ComponentOptionsMixin, import('vue').ComponentOptionsMixin, {
    /** Model Update */
    'update:modelValue': (_value?: string | Text) => true;
    /** CodeMirror ViewUpdate */
    update: (_value: ViewUpdate) => true;
    /** CodeMirror onReady */
    ready: (_value: {
        view: EditorView;
        state: EditorState;
        container: HTMLElement;
    }) => true;
    /** CodeMirror onFocus */
    focus: (_value: boolean) => true;
    /** State Changed */
    change: (_value: EditorState) => true;
    /** CodeMirror onDestroy */
    destroy: () => true;
}, string, import('vue').PublicProps, Readonly<import('vue').ExtractPropTypes<{
    /** Model value */
    modelValue: {
        type: PropType<string | Text>;
        default: string;
    };
    /**
     * Theme
     *
     * @see {@link https://codemirror.net/docs/ref/#view.EditorView^theme}
     */
    theme: {
        type: PropType<Record<string, StyleSpec>>;
        default: () => {};
    };
    /** Dark Mode */
    dark: {
        type: BooleanConstructor;
        default: boolean;
    };
    /**
     * Use Basic Setup
     *
     * @see {@link https://codemirror.net/docs/ref/#codemirror.basicSetup}
     */
    basic: {
        type: BooleanConstructor;
        default: boolean;
    };
    /**
     * Use Minimal Setup (The basic setting has priority.)
     *
     * @see {@link https://codemirror.net/docs/ref/#codemirror.minimalSetup}
     */
    minimal: {
        type: BooleanConstructor;
        default: boolean;
    };
    /**
     * Placeholder
     *
     * @see {@link https://codemirror.net/docs/ref/#view.placeholder}
     */
    placeholder: {
        type: PropType<string | HTMLElement>;
        default: undefined;
    };
    /**
     * Line wrapping
     *
     * An extension that enables line wrapping in the editor (by setting CSS white-space to pre-wrap in the content).
     *
     * @see {@link https://codemirror.net/docs/ref/#view.EditorView%5ElineWrapping}
     */
    wrap: {
        type: BooleanConstructor;
        default: boolean;
    };
    /**
     * Allow tab key indent.
     *
     * @see {@link https://codemirror.net/examples/tab/}
     */
    tab: {
        type: BooleanConstructor;
        default: boolean;
    };
    /**
     * Tab character
     */
    indentUnit: {
        type: StringConstructor;
        default: undefined;
    };
    /**
     * Allow Multiple Selection.
     *
     * @see {@link https://codemirror.net/docs/ref/#state.EditorState^allowMultipleSelections}
     */
    allowMultipleSelections: {
        type: BooleanConstructor;
        default: boolean;
    };
    /**
     * Tab size
     *
     * @see {@link https://codemirror.net/docs/ref/#state.EditorState^tabSize}
     */
    tabSize: {
        type: NumberConstructor;
        default: undefined;
    };
    /**
     * Set line break (separetor) char.
     *
     * @see {@link https://codemirror.net/docs/ref/#state.EditorState^lineSeparator}
     */
    lineSeparator: {
        type: StringConstructor;
        default: undefined;
    };
    /**
     * Readonly
     *
     * @see {@link https://codemirror.net/docs/ref/#state.EditorState^readOnly}
     */
    readonly: {
        type: BooleanConstructor;
        default: boolean;
    };
    /**
     * Disable input.
     *
     * This is the reversed value of the CodeMirror editable.
     * Similar to `readonly`, but setting this value to true disables dragging.
     *
     * @see {@link https://codemirror.net/docs/ref/#view.EditorView^editable}
     */
    disabled: {
        type: BooleanConstructor;
        default: boolean;
    };
    /**
     * Additional Extension
     *
     * @see {@link https://codemirror.net/docs/ref/#state.Extension}
     */
    extensions: {
        type: PropType<Extension[]>;
        default: () => never[];
    };
    /**
     * Language Phreses
     *
     * @see {@link https://codemirror.net/examples/translate/}
     */
    phrases: {
        type: PropType<Record<string, string>>;
        default: () => undefined;
    };
    /**
     * CodeMirror Language
     *
     * @see {@link https://codemirror.net/docs/ref/#language}
     */
    lang: {
        type: PropType<LanguageSupport>;
        default: () => undefined;
    };
    /**
     * CodeMirror Linter
     *
     * @see {@link https://codemirror.net/docs/ref/#lint.linter}
     */
    linter: {
        type: PropType<LintSource | any>;
        default: undefined;
    };
    /**
     * Linter Config
     *
     * @see {@link https://codemirror.net/docs/ref/#lint.linter^config}
     */
    linterConfig: {
        type: ObjectConstructor;
        default: () => {};
    };
    /**
     * Forces any linters configured to run when the editor is idle to run right away.
     *
     * @see {@link https://codemirror.net/docs/ref/#lint.forceLinting}
     */
    forceLinting: {
        type: BooleanConstructor;
        default: boolean;
    };
    /**
     * Show Linter Gutter
     *
     * An area to 🔴 the lines with errors will be displayed.
     * This feature is not enabled if `linter` is not specified.
     *
     * @see {@link https://codemirror.net/docs/ref/#lint.lintGutter}
     */
    gutter: {
        type: BooleanConstructor;
        default: boolean;
    };
    /**
     * Gutter Config
     *
     * @see {@link https://codemirror.net/docs/ref/#lint.lintGutter^config}
     */
    gutterConfig: {
        type: ObjectConstructor;
        default: () => undefined;
    };
    /**
     * Using tag
     */
    tag: {
        type: StringConstructor;
        default: string;
    };
    /**
     * Allows an external update to scroll the form.
     * @see {@link https://codemirror.net/docs/ref/#state.TransactionSpec.scrollIntoView}
     */
    scrollIntoView: {
        type: BooleanConstructor;
        default: boolean;
    };
}>> & Readonly<{
    onChange?: ((_value: EditorState) => any) | undefined;
    onFocus?: ((_value: boolean) => any) | undefined;
    "onUpdate:modelValue"?: ((_value?: string | Text | undefined) => any) | undefined;
    onUpdate?: ((_value: ViewUpdate) => any) | undefined;
    onReady?: ((_value: {
        view: EditorView;
        state: EditorState;
        container: HTMLElement;
    }) => any) | undefined;
    onDestroy?: (() => any) | undefined;
}>, {
    modelValue: string | Text;
    theme: Record<string, StyleSpec>;
    dark: boolean;
    basic: boolean;
    minimal: boolean;
    placeholder: string | HTMLElement;
    wrap: boolean;
    tab: boolean;
    indentUnit: string;
    allowMultipleSelections: boolean;
    tabSize: number;
    lineSeparator: string;
    readonly: boolean;
    disabled: boolean;
    extensions: Extension[];
    phrases: Record<string, string>;
    lang: LanguageSupport;
    linter: any;
    linterConfig: Record<string, any>;
    forceLinting: boolean;
    gutter: boolean;
    gutterConfig: Record<string, any>;
    tag: string;
    scrollIntoView: boolean;
}, {}, {}, {}, string, import('vue').ComponentProvideOptions, true, {}, any>;
export default _default;
