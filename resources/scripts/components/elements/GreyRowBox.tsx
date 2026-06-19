import styled from 'styled-components/macro';
import tw from 'twin.macro';

export default styled.div<{ $hoverable?: boolean }>`
    ${tw`flex rounded-md no-underline text-neutral-200 items-center bg-neutral-700 p-4 border border-neutral-500 shadow-sm transition-colors duration-150 overflow-hidden`};

    ${(props) => props.$hoverable !== false && tw`hover:border-neutral-400`};

    & .icon {
        ${tw`rounded-full w-16 flex items-center justify-center bg-neutral-600 text-neutral-300 p-3`};
    }
`;
