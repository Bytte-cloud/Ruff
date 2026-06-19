import React, { forwardRef } from 'react';
import { Form } from 'formik';
import styled from 'styled-components/macro';
import FlashMessageRender from '@/components/FlashMessageRender';
import tw from 'twin.macro';

type Props = React.DetailedHTMLProps<React.FormHTMLAttributes<HTMLFormElement>, HTMLFormElement> & {
    title?: string;
};

const Container = styled.div`
    ${tw`mx-auto w-full px-4`};
    max-width: 26rem;
`;

export default forwardRef<HTMLFormElement, Props>(({ title, ...props }, ref) => (
    <Container>
        <FlashMessageRender css={tw`mb-3`} />
        <Form {...props} ref={ref}>
            <div css={tw`bg-neutral-700 border border-neutral-500 shadow-sm rounded-lg p-8`}>
                <div css={tw`flex justify-center select-none mb-6`}>
                    <img src={'/assets/svgs/Ruff.svg'} css={tw`block h-12 w-auto`} alt={'Ruff'} />
                </div>
                {title && <h2 css={tw`text-xl text-center text-neutral-100 font-semibold mb-6`}>{title}</h2>}
                {props.children}
            </div>
        </Form>
        <p css={tw`text-center text-neutral-400 text-xs mt-6`}>
            &copy; 2015 - {new Date().getFullYear()}&nbsp;
            <a
                rel={'noopener nofollow noreferrer'}
                href={'https://Ruff.io'}
                target={'_blank'}
                css={tw`no-underline text-neutral-400 hover:text-neutral-200 transition-colors duration-150`}
            >
                Ruff Software
            </a>
        </p>
    </Container>
));
