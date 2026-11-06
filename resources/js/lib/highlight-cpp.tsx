import type { ReactNode } from 'react';

const cppKeywords = new Set([
    'alignas',
    'alignof',
    'auto',
    'bool',
    'break',
    'case',
    'catch',
    'char',
    'class',
    'concept',
    'const',
    'constexpr',
    'consteval',
    'constinit',
    'const_cast',
    'continue',
    'decltype',
    'default',
    'delete',
    'do',
    'double',
    'else',
    'enum',
    'explicit',
    'export',
    'extern',
    'false',
    'float',
    'for',
    'friend',
    'if',
    'inline',
    'int',
    'long',
    'mutable',
    'namespace',
    'new',
    'noexcept',
    'nullptr',
    'operator',
    'private',
    'protected',
    'public',
    'requires',
    'return',
    'short',
    'sizeof',
    'static',
    'static_assert',
    'static_cast',
    'struct',
    'switch',
    'template',
    'this',
    'throw',
    'true',
    'try',
    'typedef',
    'typename',
    'union',
    'unsigned',
    'using',
    'virtual',
    'void',
    'volatile',
    'while',
]);

const tokenPattern =
    /(\/\/[^\n]*|\/\*[\s\S]*?\*\/|#[A-Za-z]+|"(?:\\.|[^"\\])*"|'(?:\\.|[^'\\])*'|\b\d+(?:\.\d+)?\b|[A-Za-z_$][\w$]*)/g;

export function highlightCpp(code: string): ReactNode[] {
    return code.split(tokenPattern).map((token, index) => {
        if (token === '') {
            return null;
        }

        if (
            token.startsWith('//') ||
            token.startsWith('/*') ||
            token.startsWith('#')
        ) {
            return (
                <span key={index} className="text-slate-500 italic">
                    {token}
                </span>
            );
        }

        if (token.startsWith('"') || token.startsWith("'")) {
            return (
                <span key={index} className="text-emerald-300">
                    {token}
                </span>
            );
        }

        if (/^\d/.test(token)) {
            return (
                <span key={index} className="text-amber-300">
                    {token}
                </span>
            );
        }

        if (cppKeywords.has(token)) {
            return (
                <span key={index} className="text-sky-300">
                    {token}
                </span>
            );
        }

        return <span key={index}>{token}</span>;
    });
}
